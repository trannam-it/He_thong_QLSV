<?php
/**
 * Grade Controller
 * Quản lý điểm
 */
class GradeController extends BaseController
{
    public function index()
    {
        $this->auth->requirePermission('manage_grades');
        
        $pagination = $this->getPagination();
        $classId = $_GET['class_id'] ?? null;
        
        $where = '';
        $params = [];
        
        if ($classId) {
            $where = 'e.class_id = ?';
            $params = [$classId];
        }
        
        $query = "SELECT g.*, e.class_id, c.class_code, s.first_name, s.last_name, s.student_code, sub.subject_name 
                  FROM grades g
                  JOIN enrollments e ON g.enrollment_id = e.enrollment_id
                  JOIN students s ON e.student_id = s.student_id
                  JOIN classes c ON e.class_id = c.class_id
                  JOIN subjects sub ON c.subject_id = sub.subject_id";
        
        if ($where) $query .= " WHERE $where";
        $query .= " ORDER BY g.grade_id DESC LIMIT ? OFFSET ?";
        
        $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
        $grades = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
        $countQuery = "SELECT COUNT(*) as total FROM grades g JOIN enrollments e ON g.enrollment_id = e.enrollment_id";
        if ($where) $countQuery .= " WHERE $where";
        
        $countParams = array_values($params);
        $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

        return Response::paginate($grades, $total, $pagination['page'], $pagination['limit']);
    }

    public function show()
    {
        $this->auth->requirePermission('manage_grades');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Grade ID required', 400);

        $grade = $this->db->selectOne('grades', 'grade_id = ?', [$id]);
        if (!$grade) return Response::error('Grade not found', 404);

        return Response::success($grade);
    }

    public function store()
    {
        $this->auth->requirePermission('manage_grades');

        $data = $this->getRequestData();
        
        $rules = [
            'enrollment_id' => 'required|numeric',
            'score' => 'required|numeric'
        ];

        if (!$this->validator->validate($data, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $score = $this->parseScore($data['score'] ?? null);
        if ($score === null) {
            return Response::error('Score must be between 0 and 10 or 0 and 100', 400);
        }

        // Prevent duplicate grade for the same enrollment
        if ($this->db->count('grades', 'enrollment_id = ?', [$data['enrollment_id']]) > 0) {
            return Response::error('Enrollment already graded', 400);
        }

        // Calculate grade letter
        $gradeLetter = $this->calculateGradeLetter($score);

        $gradeId = $this->db->insert('grades', [
            'enrollment_id' => $data['enrollment_id'],
            'score' => $score,
            'grade_letter' => $gradeLetter
        ]);

        if ($gradeId) {
            $this->logAudit('CREATE', 'grades', $gradeId, null, $data);
            return Response::success(['id' => $gradeId], 'Grade created', 201);
        }

        return Response::error('Failed to create grade', 500);
    }

    public function update()
    {
        $this->auth->requirePermission('manage_grades');

        $data = $this->getRequestData();
        
        $id = $data['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Grade ID required', 400);

        $grade = $this->db->selectOne('grades', 'grade_id = ?', [$id]);
        if (!$grade) return Response::error('Grade not found', 404);

        $rules = ['score' => 'required|numeric'];

        if (!$this->validator->validate($data, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        if ($this->isLockedGrade($grade)) {
            return Response::error('Grade is locked', 403);
        }

        $score = $this->parseScore($data['score'] ?? null);
        if ($score === null) {
            return Response::error('Score must be between 0 and 10 or 0 and 100', 400);
        }

        $gradeLetter = $this->calculateGradeLetter($score);

        $updateData = ['score' => $score, 'grade_letter' => $gradeLetter];

        $this->db->update('grades', $updateData, 'grade_id = ?', [$id]);
        $this->logAudit('UPDATE', 'grades', $id, $grade, $updateData);
        
        return Response::success(null, 'Grade updated');
    }

    public function delete()
    {
        $this->auth->requirePermission('manage_grades');

        $data = $this->getRequestData();
        
        $id = $data['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Grade ID required', 400);

        $grade = $this->db->selectOne('grades', 'grade_id = ?', [$id]);
        if (!$grade) return Response::error('Grade not found', 404);

        if ($this->isLockedGrade($grade)) {
            return Response::error('Grade is locked', 403);
        }

        $this->db->delete('grades', 'grade_id = ?', [$id]);
        $this->logAudit('DELETE', 'grades', $id, $grade, null);
        
        return Response::success(null, 'Grade deleted');
    }

    /**
     * Calculate grade letter based on score
     */
    private function calculateGradeLetter($score)
    {
        // 10-point scale
        if ($score <= 10) {
            if ($score >= 9.0) return 'A+';
            if ($score >= 8.5) return 'A';
            if ($score >= 8.0) return 'B+';
            if ($score >= 7.5) return 'B';
            if ($score >= 7.0) return 'C+';
            if ($score >= 6.5) return 'C';
            if ($score >= 6.0) return 'D+';
            if ($score >= 5.5) return 'D';
            return 'F';
        }

        // 100-point scale (consistent with teacher grading)
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

    private function parseScore($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $score = floatval($value);
        if ($score < 0 || $score > 100) {
            return null;
        }

        return $score;
    }

    private function getRequestData()
    {
        if (!empty($_POST)) {
            return $_POST;
        }

        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function supportsGradeLocking()
    {
        $res = $this->db->query("SHOW COLUMNS FROM grades LIKE 'is_locked'");
        return $res instanceof mysqli_result && $res->num_rows > 0;
    }

    private function isLockedGrade($grade)
    {
        return $this->supportsGradeLocking() && !empty($grade['is_locked']);
    }

    public function lock()
    {
        $this->auth->requirePermission('manage_grades');

        $data = $this->getRequestData();
        $id = $data['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Grade ID required', 400);

        if (!$this->supportsGradeLocking()) {
            return Response::error('Locking is not supported. Add is_locked column to grades.', 400);
        }

        $grade = $this->db->selectOne('grades', 'grade_id = ?', [$id]);
        if (!$grade) return Response::error('Grade not found', 404);

        if (!empty($grade['is_locked'])) {
            return Response::success(null, 'Grade already locked');
        }

        $this->db->update('grades', ['is_locked' => 1], 'grade_id = ?', [$id]);
        $this->logAudit('UPDATE', 'grades', $id, $grade, ['is_locked' => 1]);

        return Response::success(null, 'Grade locked');
    }

    public function studentsByClass()
    {
        $this->auth->requirePermission('manage_grades');

        $classId = $_GET['class_id'] ?? null;
        if (!$classId) return Response::error('class_id required', 400);

        $sql = "SELECT 
                    e.enrollment_id,
                    s.student_code,
                    CONCAT(s.last_name, ' ', s.first_name) AS full_name
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                WHERE e.class_id = ?
                  AND e.status IN ('Registered','Completed')";

        return Response::success(
            $this->db->query($sql, [$classId])->fetch_all(MYSQLI_ASSOC)
        );
    }

    public function import()
    {
        $this->auth->requirePermission('manage_grades');

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return Response::error('File CSV bắt buộc', 400);
        }

        $tmpPath = $_FILES['file']['tmp_name'];
        $handle  = fopen($tmpPath, 'r');
        if (!$handle) {
            return Response::error('Không đọc được file', 500);
        }

        $inserted = 0;
        $skipped  = 0;
        $rowNum   = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($rowNum === 1) continue; // bỏ dòng tiêu đề

            if (count($row) < 2) { $skipped++; continue; }

            $enrollment_id = intval($row[0]);
            $score = $this->parseScore($row[1]);

            if (!$enrollment_id || $score === null) { $skipped++; continue; }

            // Bỏ qua nếu đã có điểm
            if ($this->db->count('grades', 'enrollment_id = ?', [$enrollment_id]) > 0) {
                $skipped++;
                continue;
            }

            $this->db->insert('grades', [
                'enrollment_id' => $enrollment_id,
                'score'         => $score,
                'grade_letter'  => $this->calculateGradeLetter($score)
            ]);
            $inserted++;
        }

        fclose($handle);

        return Response::success(
            ['inserted' => $inserted, 'skipped' => $skipped],
            "Import thành công: $inserted bản ghi, $skipped bỏ qua"
        );
    }
}

?>