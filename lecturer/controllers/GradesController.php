<?php
/**
 * GradesController - Nhập điểm sinh viên
 */

class GradesController extends BaseLecturerController
{
    public function index(): void
    {
        $lid     = $this->lecturerId;
        $classId = (int)($_GET['class_id'] ?? $_POST['class_id'] ?? 0);
        $openAdd = isset($_GET['add']);

        // Xử lý POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action  = $_POST['action'] ?? '';
            $classId = (int)($_POST['class_id'] ?? 0);

            // Xác minh lớp thuộc giảng viên này
            $classInfo = $classId ? $this->model->getClassOfLecturer($classId, $lid) : null;
            if (!$classInfo) {
                $_SESSION['grade_error'] = 'Lớp không hợp lệ hoặc không có quyền truy cập.';
                header('Location: ' . LecturerRouter::url('grades') . '&class_id=' . $classId);
                exit;
            }

            match ($action) {
                'save_grades'     => $this->handleSaveGrades($classId),
                'add_student'     => $this->handleAddStudent($classId),
                'remove_student'  => $this->handleRemoveStudent($classId),
                default           => null,
            };
        }

        // Flash messages
        $flashSuccess = $this->getFlash('grade_success');
        $flashError   = $this->getFlash('grade_error');

        // Load dữ liệu
        $myClasses  = $this->model->getAllLecturerClasses($lid);
        $classInfo  = $classId ? $this->model->getClassOfLecturer($classId, $lid) : null;
        $students   = [];
        $gradeStats = [];

        if ($classInfo) {
            $students   = $this->model->getStudentsWithGrades($classId);
            $gradeStats = $this->model->getGradeStats($classId);
        }

        $this->render('grades/index.php', compact(
            'myClasses', 'classInfo', 'classId', 'students',
            'gradeStats', 'openAdd', 'flashSuccess', 'flashError'
        ));
    }

    private function handleSaveGrades(int $classId): void
    {
        $scores = $_POST['scores'] ?? [];
        $saved  = 0;

        foreach ($scores as $enrollId => $scoreRaw) {
            $enrollId = (int)$enrollId;
            $score    = ($scoreRaw === '') ? null : floatval($scoreRaw);

            if ($score !== null && ($score < 0 || $score > 100)) {
                continue;
            }

            $this->model->saveGrade($enrollId, $score);
            $saved++;
        }

        $_SESSION['grade_success'] = "Đã lưu điểm cho $saved sinh viên!";
        header('Location: ' . LecturerRouter::url('grades') . '&class_id=' . $classId);
        exit;
    }

    private function handleAddStudent(int $classId): void
    {
        $studentCode = trim($_POST['student_code'] ?? '');
        if (!$studentCode) {
            $_SESSION['grade_error'] = 'Vui lòng nhập mã sinh viên.';
            header('Location: ' . LecturerRouter::url('grades') . '&class_id=' . $classId . '&add=1');
            exit;
        }

        $result = $this->model->addStudentToClass($classId, $studentCode);
        if ($result['success']) {
            $_SESSION['grade_success'] = $result['message'];
        } else {
            $_SESSION['grade_error'] = $result['message'];
        }

        header('Location: ' . LecturerRouter::url('grades') . '&class_id=' . $classId);
        exit;
    }

    private function handleRemoveStudent(int $classId): void
    {
        $enrollId = (int)($_POST['enrollment_id'] ?? 0);
        if ($this->model->removeStudentFromClass($enrollId, $classId)) {
            $_SESSION['grade_success'] = 'Đã hủy đăng ký sinh viên khỏi lớp.';
        } else {
            $_SESSION['grade_error'] = 'Không tìm thấy bản ghi đăng ký.';
        }

        header('Location: ' . LecturerRouter::url('grades') . '&class_id=' . $classId);
        exit;
    }
}
