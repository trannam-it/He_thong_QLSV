<?php
/**
 * RegisterController - Đăng ký lớp dạy
 */

class RegisterController extends BaseLecturerController
{
    public function index(): void
    {
        $lid = $this->lecturerId;
        $flash = ['type' => '', 'msg' => ''];

        // Xử lý POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $semester  = $_POST['semester'] ?? '';
            $year      = (int)($_POST['year'] ?? 0);

            $validSemesters = ['Spring', 'Summer', 'Fall'];
            $errors = [];

            if (!$subjectId)                            $errors[] = 'Vui lòng chọn môn học.';
            if (!in_array($semester, $validSemesters))  $errors[] = 'Học kỳ không hợp lệ.';
            if ($year < 2020 || $year > 2040)           $errors[] = 'Năm học không hợp lệ.';

            if (empty($errors)) {
                if ($this->model->hasRegisteredClass($lid, $subjectId, $semester, $year)) {
                    $errors[] = 'Bạn đã đăng ký giảng dạy môn này trong học kỳ và năm này rồi.';
                }
            }

            if (empty($errors)) {
                // Tìm lớp chưa có giảng viên
                $freeClass = $this->model->findUnassignedClass($subjectId, $semester, $year);

                if ($freeClass) {
                    $this->model->assignLecturerToClass($freeClass['class_id'], $lid);
                    $_SESSION['reg_success'] = "Đã nhận lớp <strong>{$freeClass['class_code']}</strong> thành công!";
                } else {
                    $result = $this->model->createNewClass($subjectId, $lid, $semester, $year);
                    if ($result['success']) {
                        $_SESSION['reg_success'] = "Đã tạo và đăng ký lớp <strong>{$result['class_code']}</strong> thành công!";
                    } else {
                        $_SESSION['reg_error'] = 'Lỗi khi tạo lớp mới. Mã lớp có thể đã tồn tại.';
                    }
                }

                header('Location: ' . LecturerRouter::url('register'));
                exit;
            }

            $flash = ['type' => 'danger', 'msg' => implode('<br>', $errors)];
        }

        // Đọc flash nếu chưa có
        if (!$flash['msg']) {
            if (!empty($_SESSION['reg_success'])) {
                $flash = ['type' => 'success', 'msg' => $_SESSION['reg_success']];
            } elseif (!empty($_SESSION['reg_error'])) {
                $flash = ['type' => 'danger',  'msg' => $_SESSION['reg_error']];
            }
            unset($_SESSION['reg_success'], $_SESSION['reg_error']);
        }

        $subjects    = $this->model->getAllSubjects();
        $myClasses   = $this->model->getAllLecturerClasses($lid);
        $currentYear = (int)date('Y');
        $currentSem  = $this->getCurrentSemester();

        $this->render('register/index.php', compact(
            'flash', 'subjects', 'myClasses', 'currentYear', 'currentSem'
        ));
    }

    private function getCurrentSemester(): string
    {
        $m = (int)date('n');
        if ($m <= 5) return 'Spring';
        if ($m <= 8) return 'Summer';
        return 'Fall';
    }
}
