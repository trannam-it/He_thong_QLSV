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
        
        $query = "SELECT g.*, s.first_name, s.last_name, s.student_code, sub.subject_name 
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
        
        $rules = [
            'enrollment_id' => 'required|numeric',
            'score' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        // Check score is between 0-10
        $score = floatval($_POST['score']);
        if ($score < 0 || $score > 10) {
            return Response::error('Score must be between 0 and 10', 400);
        }

        // Calculate grade letter
        $gradeLetter = $this->calculateGradeLetter($score);

        $gradeId = $this->db->insert('grades', [
            'enrollment_id' => $_POST['enrollment_id'],
            'score' => $score,
            'grade_letter' => $gradeLetter
        ]);

        if ($gradeId) {
            $this->logAudit('CREATE', 'grades', $gradeId, null, $_POST);
            return Response::success(['id' => $gradeId], 'Grade created', 201);
        }

        return Response::error('Failed to create grade', 500);
    }

    public function update()
    {
        $this->auth->requirePermission('manage_grades');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Grade ID required', 400);

        $grade = $this->db->selectOne('grades', 'grade_id = ?', [$id]);
        if (!$grade) return Response::error('Grade not found', 404);

        $rules = ['score' => 'required|numeric'];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $score = floatval($_POST['score']);
        if ($score < 0 || $score > 10) {
            return Response::error('Score must be between 0 and 10', 400);
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
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Grade ID required', 400);

        $grade = $this->db->selectOne('grades', 'grade_id = ?', [$id]);
        if (!$grade) return Response::error('Grade not found', 404);

        $this->db->delete('grades', 'grade_id = ?', [$id]);
        $this->logAudit('DELETE', 'grades', $id, $grade, null);
        
        return Response::success(null, 'Grade deleted');
    }

    /**
     * Calculate grade letter based on score
     */
    private function calculateGradeLetter($score)
    {
        if ($score >= 8.5) return 'A';
        if ($score >= 7.5) return 'B+';
        if ($score >= 7.0) return 'B';
        if ($score >= 6.5) return 'C+';
        if ($score >= 6.0) return 'C';
        if ($score >= 5.0) return 'D';
        return 'F';
    }


public function import()
{
    $this->auth->requirePermission('import_grades');

    if (!isset($_FILES['file'])) {
        return Response::error('File required', 400);
    }

    require_once __DIR__ . '/../libs/SimpleXLSX.php';

    $xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name']);
    if (!$xlsx) {
        return Response::error('Invalid Excel file', 400);
    }

    $rows = $xlsx->rows();
    unset($rows[0]); // bỏ header

    $inserted = 0;

    foreach ($rows as $row) {
        [$enrollment_id, $score] = $row;

        if ($score < 0 || $score > 10) continue;

        $this->db->insert('grades', [
            'enrollment_id' => $enrollment_id,
            'score' => $score,
            'grade_letter' => $this->calculateGradeLetter($score)
        ]);
        $inserted++;
    }

    return Response::success(['inserted' => $inserted], 'Import thành công');
}
}

?>