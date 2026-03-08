<?php
/**
 * Lecturer Controller
 * Quản lý giảng viên
 */
class LecturerController extends BaseController
{

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

    // Học vị filter
    if (!empty($_GET['degree'])) {
        $conditions[] = 'l.degree = ?';
        $params[] = $_GET['degree'];
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
        'first_name' => 'required',
        'last_name'  => 'required',
        'email'      => 'required|email',
        'degree'     => 'required',
        'faculty_id' => 'required|numeric'
    ];

    if (!$this->validator->validate($_POST, $rules)) {
        return Response::error('Validation failed', 400, $this->validator->getErrors());
    }

    // ====== TỰ ĐỘNG TẠO MÃ GV ======
    $last = $this->db->query("
        SELECT lecturer_code
        FROM lecturers
        ORDER BY lecturer_id DESC
        LIMIT 1
    ")->fetch_assoc();

    $num = 0;
    if ($last && preg_match('/(\d+)$/', $last['lecturer_code'], $m)) {
        $num = intval($m[1]);
    }

    $num++;
    $lecturer_code = 'GV' . str_pad($num, 3, '0', STR_PAD_LEFT);

    // ====== INSERT LECTURER ======
    $lecturerId = $this->db->insert('lecturers', [
        'lecturer_code' => $lecturer_code,
        'first_name'    => $_POST['first_name'],
        'last_name'     => $_POST['last_name'],
        'email'         => $_POST['email'],
        'phone'         => $_POST['phone'] ?? null,
        'degree'        => $_POST['degree'],
        'faculty_id'    => $_POST['faculty_id']
    ]);

    if (!$lecturerId) {
        return Response::error('Không thể tạo giảng viên', 500);
    }

    // ====== TẠO USER TƯƠNG ỨNG ======
    $username = $lecturer_code;
    $defaultPassword = '123456';

    $userId = $this->db->insert('users', [
        'username'      => $username,
        'email'         => $_POST['email'],
        'password_hash' => password_hash($defaultPassword, PASSWORD_BCRYPT),
        'is_active'     => 1
    ]);

    if ($userId) {

        // Gán user vào lecturer
        $this->db->update('lecturers',
            ['user_id' => $userId],
            'lecturer_id = ?',
            [$lecturerId]
        );

        // Gán role teacher
        $role = $this->db->selectOne('roles', 'code = ?', ['teacher']);
        if ($role) {
            $this->db->insert('user_roles', [
                'user_id' => $userId,
                'role_id' => $role['id']
            ]);
        }
    }

    return Response::success([
        'lecturer_id' => $lecturerId,
        'username'    => $username,
        'password'    => $defaultPassword
    ], 'Tạo giảng viên thành công', 201);
}


    /**
     * Return next lecturer code (GV###)
     */
    public function nextCode()
    {
        $this->auth->requirePermission('manage_lecturers');
        $last = $this->db->query("SELECT lecturer_code FROM lecturers ORDER BY lecturer_id DESC LIMIT 1")->fetch_assoc();
        $num = 0;
        if ($last && preg_match('/(\d+)$/', $last['lecturer_code'], $m)) {
            $num = intval($m[1]);
        }
        $num++;
        $next = 'GV' . str_pad($num, 3, '0', STR_PAD_LEFT);
        return Response::success(['next_code' => $next]);
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

// class LecturerController extends BaseController
// {
//     public function index()
//     {
//         $this->auth->requirePermission('manage_lecturers');

//         $pagination = $this->getPagination();

//         $conditions = [];
//         $params = [];

//         if (!empty($_GET['search'])) {
//             $search = '%' . $_GET['search'] . '%';
//             $conditions[] = '(l.lecturer_code LIKE ?
//                               OR l.first_name LIKE ?
//                               OR l.last_name LIKE ?
//                               OR l.email LIKE ?)';
//             $params = array_merge($params, [$search,$search,$search,$search]);
//         }

//         if (!empty($_GET['faculty_id'])) {
//             $conditions[] = 'l.faculty_id = ?';
//             $params[] = $_GET['faculty_id'];
//         }

//         if (!empty($_GET['degree'])) {
//             $conditions[] = 'l.degree = ?';
//             $params[] = $_GET['degree'];
//         }

//         $where = $conditions ? 'WHERE '.implode(' AND ',$conditions) : '';

//         $query = "
//             SELECT l.*, f.faculty_name
//             FROM lecturers l
//             LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
//             $where
//             ORDER BY l.lecturer_id DESC
//             LIMIT ? OFFSET ?
//         ";

//         $dataParams = array_merge($params,[
//             $pagination['limit'],
//             $pagination['offset']
//         ]);

//         $lecturers = $this->db
//             ->query($query,$dataParams)
//             ->fetch_all(MYSQLI_ASSOC);

//         $total = $this->db
//             ->query("SELECT COUNT(*) as total FROM lecturers l $where",$params)
//             ->fetch_assoc()['total'];

//         return Response::paginate(
//             $lecturers,
//             $total,
//             $pagination['page'],
//             $pagination['limit']
//         );
//     }

//     public function getNextCode()
//     {
//         $last = $this->db->query("
//             SELECT lecturer_code
//             FROM lecturers
//             ORDER BY lecturer_id DESC
//             LIMIT 1
//         ")->fetch_assoc();

//         if (!$last) {
//             return Response::success(['code'=>'GV001']);
//         }

//         $number = intval(substr($last['lecturer_code'],2)) + 1;
//         $newCode = 'GV'.str_pad($number,3,'0',STR_PAD_LEFT);

//         return Response::success(['code'=>$newCode]);
//     }

//     public function store()
//     {
//         $this->auth->requirePermission('manage_lecturers');

//         $lecturerId = $this->db->insert('lecturers',[
//             'lecturer_code'=>$_POST['lecturer_code'],
//             'first_name'=>$_POST['first_name'],
//             'last_name'=>$_POST['last_name'],
//             'email'=>$_POST['email'],
//             'phone'=>$_POST['phone'] ?? null,
//             'degree'=>$_POST['degree'],
//             'faculty_id'=>$_POST['faculty_id']
//         ]);

//         if ($lecturerId) {

//             // TỰ ĐỘNG TẠO USER
//             $this->db->insert('users',[
//                 'username'=>$_POST['lecturer_code'],
//                 'password'=>password_hash('123456',PASSWORD_DEFAULT),
//                 'role'=>'lecturer',
//                 'reference_id'=>$lecturerId
//             ]);

//             return Response::success(null,'Lecturer created');
//         }

//         return Response::error('Create failed',500);
//     }

//     public function statistics()
//     {
//         $byFaculty = $this->db->query("
//             SELECT f.faculty_name, COUNT(*) total
//             FROM lecturers l
//             LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
//             GROUP BY f.faculty_name
//         ")->fetch_all(MYSQLI_ASSOC);

//         $byDegree = $this->db->query("
//             SELECT degree, COUNT(*) total
//             FROM lecturers
//             GROUP BY degree
//         ")->fetch_all(MYSQLI_ASSOC);

//         $subjects = $this->db->query("
//             SELECT l.lecturer_code,
//                    CONCAT(l.first_name,' ',l.last_name) name,
//                    COUNT(DISTINCT c.subject_id) total_subjects
//             FROM lecturers l
//             LEFT JOIN classes c ON l.lecturer_id = c.lecturer_id
//             GROUP BY l.lecturer_id
//         ")->fetch_all(MYSQLI_ASSOC);

//         return Response::success([
//             'byFaculty'=>$byFaculty,
//             'byDegree'=>$byDegree,
//             'subjects'=>$subjects
//         ]);
//     }
// }

?>