<?php
/**
 * Subject Controller
 * Quản lý môn học
 */
class SubjectController extends BaseController
{
//     public function index()
//     {
//         // Check permission - skip for now if not logged in
//         try {
//             $this->auth->requirePermission('manage_subjects');
//         } catch (Exception $e) {
//             // Allow localhost bypass for development
//             if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '::1') {
//                 throw $e;
//             }
//         }
        
//         $pagination = $this->getPagination();
//         $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
        
//         $where = '';
//         $params = [];
        
//         if ($search) {
//             $where = '(s.subject_code LIKE ? OR s.subject_name LIKE ?)';
//             $params = [$search, $search];
//         }
        
//         $query = "SELECT s.*, f.faculty_code, f.faculty_name, 
//                          prereq.subject_code as prerequisite_code, prereq.subject_name as prerequisite_name
//                   FROM subjects s
//                   LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
//                   LEFT JOIN subjects prereq ON s.prerequisite_code = prereq.subject_code";
//         if ($where) $query .= " WHERE $where";
//         $query .= " ORDER BY s.subject_id DESC LIMIT ? OFFSET ?";
        
//         $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
//         $result = $this->db->query($query, $allParams);
        
//         if (!$result) {
//             throw new Exception('Failed to load subjects: ' . $this->db->error);
//         }
        
//         $subjects = $result->fetch_all(MYSQLI_ASSOC);
//         if ($subjects === null) $subjects = [];
        
//         $countQuery = "SELECT COUNT(*) as total FROM subjects s";
//         if ($where) $countQuery .= " WHERE $where";
        
//         $countParams = array_values($params);
//         $countResult = $this->db->query($countQuery, $countParams);
        
//         if (!$countResult) {
//             throw new Exception('Failed to count subjects: ' . $this->db->error);
//         }
        
//         $countRow = $countResult->fetch_assoc();
//         $total = $countRow ? $countRow['total'] : 0;

//         return Response::paginate($subjects, $total, $pagination['page'], $pagination['limit']);
//     }

//     public function show()
//     {
//         $this->auth->requirePermission('manage_subjects');
        
//         $id = $_GET['id'] ?? null;
//         if (!$id) return Response::error('Subject ID required', 400);

//         $query = "SELECT s.*, f.faculty_name, 
//                          prereq.subject_code as prerequisite_code, prereq.subject_name as prerequisite_name
//                   FROM subjects s
//                   LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
//                   LEFT JOIN subjects prereq ON s.prerequisite_code = prereq.subject_code
//                   WHERE s.subject_id = ?";
        
//         $result = $this->db->query($query, [$id]);
//         if (!$result) {
//             throw new Exception('Failed to load subject: ' . $this->db->error);
//         }
        
//         $subject = $result->fetch_assoc();
        
//         if (!$subject) return Response::error('Subject not found', 404);

//         return Response::success($subject);
//     }

//     public function store()
//     {
//         $this->auth->requirePermission('manage_subjects');
        
//         $rules = [
//             'subject_code' => 'required|min:2',
//             'subject_name' => 'required|min:3',
//             'credit_hours' => 'required|numeric|min:1|max:12',
//             'faculty_id' => 'required|numeric'
//         ];

//         if (!$this->validator->validate($_POST, $rules)) {
//             return Response::error('Validation failed', 400, $this->validator->getErrors());
//         }

//         if ($this->db->count('subjects', 'subject_code = ?', [$_POST['subject_code']]) > 0) {
//             return Response::error('Subject code already exists', 400);
//         }

//         // Kiểm tra faculty tồn tại
//         if (!$this->db->selectOne('faculties', 'faculty_id = ?', [$_POST['faculty_id']])) {
//             return Response::error('Faculty not found', 404);
//         }

//         // Kiểm tra prerequisite nếu có
//         if (!empty($_POST['prerequisite_code'])) {
//             $prereq = $this->db->selectOne('subjects', 'subject_code = ?', [$_POST['prerequisite_code']]);
//             if (!$prereq) {
//                 return Response::error('Prerequisite subject not found', 404);
//             }
//         }

//         $subjectId = $this->db->insert('subjects', [
//             'subject_code' => $_POST['subject_code'],
//             'subject_name' => $_POST['subject_name'],
//             'credit_hours' => $_POST['credit_hours'],
//             'faculty_id' => $_POST['faculty_id'],
//             'description' => $_POST['description'] ?? null,
//             'prerequisite_code' => $_POST['prerequisite_code'] ?? null
//         ]);

//         if ($subjectId) {
//             $this->logAudit('CREATE', 'subjects', $subjectId, null, $_POST);
//             return Response::success(['id' => $subjectId], 'Subject created', 201);
//         }

//         return Response::error('Failed to create subject', 500);
//     }

//     public function update()
//     {
//         $this->auth->requirePermission('manage_subjects');
        
//         $id = $_POST['subject_id'] ?? null;
//         if (!$id) return Response::error('Subject ID required', 400);

//         $subject = $this->db->selectOne('subjects', 'subject_id = ?', [$id]);
//         if (!$subject) return Response::error('Subject not found', 404);

//         $rules = [
//             'subject_code' => 'required|min:2',
//             'subject_name' => 'required|min:3',
//             'credit_hours' => 'required|numeric|min:1|max:12',
//             'faculty_id' => 'required|numeric'
//         ];

//         if (!$this->validator->validate($_POST, $rules)) {
//             return Response::error('Validation failed', 400, $this->validator->getErrors());
//         }

//         // Kiểm tra mã trùng lặp (ngoại trừ chính nó)
//         if ($subject['subject_code'] !== $_POST['subject_code']) {
//             if ($this->db->count('subjects', 'subject_code = ?', [$_POST['subject_code']]) > 0) {
//                 return Response::error('Subject code already exists', 400);
//             }
//         }

//         // Kiểm tra faculty
//         if (!$this->db->selectOne('faculties', 'faculty_id = ?', [$_POST['faculty_id']])) {
//             return Response::error('Faculty not found', 404);
//         }

//         // Kiểm tra prerequisite
//         if (!empty($_POST['prerequisite_code'])) {
//             $prereq = $this->db->selectOne('subjects', 'subject_code = ?', [$_POST['prerequisite_code']]);
//             if (!$prereq) {
//                 return Response::error('Prerequisite subject not found', 404);
//             }
//         }

//         $oldData = $subject;
//         $newData = [
//             'subject_code' => $_POST['subject_code'],
//             'subject_name' => $_POST['subject_name'],
//             'credit_hours' => $_POST['credit_hours'],
//             'faculty_id' => $_POST['faculty_id'],
//             'description' => $_POST['description'] ?? null,
//             'prerequisite_code' => $_POST['prerequisite_code'] ?? null
//         ];

//         if ($this->db->update('subjects', $newData, 'subject_id = ?', [$id])) {
//             $this->logAudit('UPDATE', 'subjects', $id, $oldData, $newData);
//             return Response::success(null, 'Subject updated');
//         }

//         return Response::error('Failed to update subject', 500);
//     }

//     public function delete()
//     {
//         $this->auth->requirePermission('manage_subjects');
        
//         $id = $_POST['subject_id'] ?? null;
//         if (!$id) return Response::error('Subject ID required', 400);

//         $subject = $this->db->selectOne('subjects', 'subject_id = ?', [$id]);
//         if (!$subject) return Response::error('Subject not found', 404);

//         // Kiểm tra môn này có được sử dụng không
//         $usageCount = $this->db->count('classes', 'subject_id = ?', [$id]);
//         if ($usageCount > 0) {
//             return Response::error('Cannot delete: Subject is used in ' . $usageCount . ' class(es)', 400);
//         }

//         if ($this->db->delete('subjects', 'subject_id = ?', [$id])) {
//             $this->logAudit('DELETE', 'subjects', $id, $subject, null);
//             return Response::success(null, 'Subject deleted');
//         }

//         return Response::error('Failed to delete subject', 500);
//     }



     /* ======================================================
       INDEX - Danh sách + Search + Filter + Sort + Paging
    ====================================================== */
    public function index()
    {
        $this->auth->requirePermission('manage_subjects');

        $page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $search     = $_GET['search'] ?? '';
        $faculty_id = $_GET['faculty_id'] ?? '';
        $sort       = $_GET['sort'] ?? 'subject_id';
        $order      = strtoupper($_GET['order'] ?? 'DESC');

        // $allowedSort = ['subject_code', 'subject_name', 'credit_hours'];
        $allowedSort = ['subject_id','subject_code','subject_name','credit_hours'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'subject_id';
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(s.subject_code LIKE ? OR s.subject_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($faculty_id)) {
            $where[] = "s.faculty_id = ?";
            $params[] = $faculty_id;
        }

        $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT s.*, f.faculty_name,
                       p.subject_name AS prerequisite_name
                FROM subjects s
                LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
                LEFT JOIN subjects p ON s.prerequisite_code = p.subject_code
                $whereSQL
                ORDER BY $sort $order
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        $result = $this->db->query($sql, $params);
        $subjects = $result->fetch_all(MYSQLI_ASSOC);

        // COUNT
        $countSql = "SELECT COUNT(*) as total FROM subjects s $whereSQL";
        $countResult = $this->db->query($countSql, array_slice($params, 0, count($params)-2));
        $total = $countResult->fetch_assoc()['total'];

        return Response::paginate($subjects, $total, $page, $limit);
    }

    /* ======================================================
       SHOW
    ====================================================== */
    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error("ID required", 400);

        $subject = $this->db->selectOne('subjects', 'subject_id = ?', [$id]);
        if (!$subject) return Response::error("Subject not found", 404);

        return Response::success($subject);
    }

    /* ======================================================
       STORE
    ====================================================== */
    public function store()
    {
        $this->auth->requirePermission('manage_subjects');

        $data = $_POST;

        if (empty($data['subject_code']) ||
            empty($data['subject_name']) ||
            empty($data['credit_hours']) ||
            empty($data['faculty_id'])) {
            return Response::error("Missing required fields", 400);
        }

        if ($this->db->count('subjects', 'subject_code = ?', [$data['subject_code']]) > 0) {
            return Response::error("Subject code already exists", 400);
        }

        // Không cho chính nó làm tiên quyết
        if (!empty($data['prerequisite_code']) &&
            $data['prerequisite_code'] === $data['subject_code']) {
            return Response::error("Cannot set itself as prerequisite", 400);
        }

        // Kiểm tra vòng lặp
        if (!empty($data['prerequisite_code'])) {
            if ($this->checkCircular($data['subject_code'], $data['prerequisite_code'])) {
                return Response::error("Circular prerequisite detected", 400);
            }
        }

        $id = $this->db->insert('subjects', [
            'subject_code'      => $data['subject_code'],
            'subject_name'      => $data['subject_name'],
            'credit_hours'      => $data['credit_hours'],
            'faculty_id'        => $data['faculty_id'],
            'description'       => $data['description'] ?? null,
            'prerequisite_code' => $data['prerequisite_code'] ?? null
        ]);

        $this->logAudit('CREATE', 'subjects', $id, null, $data);

        return Response::success(['id' => $id], "Created", 201);
    }

    /* ======================================================
       UPDATE
    ====================================================== */
    public function update()
    {
        $this->auth->requirePermission('manage_subjects');
        $id = $_POST['subject_id'] ?? null;
        if (!$id) return Response::error("ID required", 400);

        $old = $this->db->selectOne('subjects', 'subject_id = ?', [$id]);
        if (!$old) return Response::error("Not found", 404);

        $data = $_POST;

        if (!empty($data['prerequisite_code']) &&
            $data['prerequisite_code'] === $data['subject_code']) {
            return Response::error("Cannot set itself as prerequisite", 400);
        }

        if (!empty($data['prerequisite_code'])) {
            if ($this->checkCircular($data['subject_code'], $data['prerequisite_code'])) {
                return Response::error("Circular prerequisite detected", 400);
            }
        }

        $updateData = [
            'subject_code'      => $data['subject_code'],
            'subject_name'      => $data['subject_name'],
            'credit_hours'      => $data['credit_hours'],
            'faculty_id'        => $data['faculty_id'],
            'description'       => $data['description'] ?? null,
            'prerequisite_code' => $data['prerequisite_code'] ?? null
        ];

        $this->db->update('subjects', $updateData, 'subject_id = ?', [$id]);
        $this->logAudit('UPDATE', 'subjects', $id, $old, $updateData);

        return Response::success(null, "Updated");
    }

    /* ======================================================
       DELETE
    ====================================================== */
    public function delete()
    {

        $this->auth->requirePermission('manage_subjects');
        $id = $_POST['subject_id'] ?? null;
        if (!$id) return Response::error("ID required", 400);

        $subject = $this->db->selectOne('subjects', 'subject_id = ?', [$id]);
        if (!$subject) return Response::error("Not found", 404);

        // Đang được dùng trong lớp
        if ($this->db->count('classes', 'subject_id = ?', [$id]) > 0) {
            return Response::error("Subject is used in classes", 400);
        }

        // Là tiên quyết của môn khác
        if ($this->db->count('subjects', 'prerequisite_code = ?', [$subject['subject_code']]) > 0) {
            return Response::error("Cannot delete. Used as prerequisite", 400);
        }

        $this->db->delete('subjects', 'subject_id = ?', [$id]);
        $this->logAudit('DELETE', 'subjects', $id, $subject, null);

        return Response::success(null, "Deleted");
    }

    /* ======================================================
       CHECK CIRCULAR
    ====================================================== */
    private function checkCircular($subjectCode, $prereqCode)
    {
        $current = $prereqCode;

        while ($current) {
            if ($current === $subjectCode) return true;

            $row = $this->db->selectOne('subjects', 'subject_code = ?', [$current]);
            if (!$row || empty($row['prerequisite_code'])) break;

            $current = $row['prerequisite_code'];
        }

        return false;
    }
    // /* ======================================================
    //    GET ALL - For dropdowns  



    public function getAll()
    {
        // Skip permission check for dropdown list
        $query = "SELECT subject_id, subject_code, subject_name, credit_hours, 
                         faculty_id, prerequisite_code
                  FROM subjects ORDER BY subject_name ASC";
        
        $result = $this->db->query($query);
        if (!$result) {
            throw new Exception('Failed to load subjects: ' . $this->db->error);
        }
        
        $subjects = $result->fetch_all(MYSQLI_ASSOC);
        if ($subjects === null) $subjects = [];
        
        return Response::success($subjects);
    }
}

