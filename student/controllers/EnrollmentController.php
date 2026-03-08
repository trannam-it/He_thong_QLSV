<?php
/**
 * EnrollmentController - Đăng ký / hủy học phần
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'enrollment.view'
 *   [L2] Controller: $this->requirePermission('enrollment.view') tại index()
 *   [L3] Action: $this->requirePermission('enrollment.register') / 'enrollment.cancel'
 */
// getAvailableClasses()
// getMyEnrollments
class EnrollmentController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('enrollment.view');

        $success = $this->getFlash('success') ?? '';
        $error   = $this->getFlash('error')   ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'register') {
                [$success, $error] = $this->handleRegister();
            } elseif ($action === 'cancel') {
                [$success, $error] = $this->handleCancel();
            }
        }

        

//         var_dump($studentId);
// exit;
        $myEnrollments = $this->model->getMyEnrollments($this->studentId);
        // var_dump($myEnrollments);
        // exit;
        $available     = $this->model->getAvailableClasses($this->studentId, (int)date('Y'));


        // Debug: Kiểm tra dữ liệu lớp học phần có sẵn
        // var_dump($available);
        // exit;

        $cntRegistered = count(array_filter($myEnrollments, fn($e) => $e['status'] === 'Enrolled'));
        $cntCompleted  = count(array_filter($myEnrollments, fn($e) => $e['status'] === 'Completed'));
        $totalCredits  = array_sum(array_map(
            fn($e) => $e['credit_hours'] ?? 0,
            array_filter($myEnrollments, fn($e) => in_array($e['status'], ['Enrolled', 'Completed']))
        ));

        $this->render('enrollment/index.php', [
            'myEnrollments' => $myEnrollments,
            'available'     => $available,
            'cntRegistered' => $cntRegistered,
            'cntCompleted'  => $cntCompleted,
            'totalCredits'  => $totalCredits,
            'success'       => $success,
            'error'         => $error,
            'pageTitle'     => 'Đăng ký học phần',
            // Truyền quyền cho view để ẩn/hiện nút
            'canRegister'   => $this->auth->hasPermission('enrollment.register'),
            'canCancel'     => $this->auth->hasPermission('enrollment.cancel'),
        ]);
    }

    // private function handleRegister(): array
    // {
    //     // [LAYER 3] Action-level permission check
    //     if (!$this->auth->hasPermission('enrollment.register')) {
    //         return ['', 'Bạn không có quyền đăng ký học phần.'];
    //     }

    //     $classId = (int)($_POST['class_id'] ?? 0);

    //     if ($classId <= 0) {
    //         return ['', 'Mã lớp học phần không hợp lệ.'];
    //     }
    //     if (!$this->model->classExists($classId)) {
    //         return ['', 'Lớp học phần không tồn tại.'];
    //     }
    //     if ($this->model->isAlreadyEnrolled($this->studentId, $classId)) {
    //         return ['', 'Bạn đã đăng ký lớp học phần này rồi.'];
    //     }
    //     if ($this->model->registerClass($this->studentId, $classId)) {
    //         return ['Đăng ký học phần thành công!', ''];
    //     }
    //     return ['', 'Lỗi khi đăng ký: ' . $this->conn->error];
    // }

    private function handleRegister(): array
    {
        if (!$this->auth->hasPermission('enrollment.register')) {
            return ['', 'Bạn không có quyền đăng ký học phần.'];
        }

        $classId = (int)($_POST['class_id'] ?? 0);

        if ($classId <= 0) {
            return ['', 'Mã lớp học phần không hợp lệ.'];
        }
        if (!$this->model->classExists($classId)) {
            return ['', 'Lớp học phần không tồn tại.'];
        }
        if ($this->model->isAlreadyEnrolled($this->studentId, $classId)) {
            return ['', 'Bạn đã đăng ký lớp học phần này rồi.'];
        }

        $result = $this->model->registerClass($this->studentId, $classId);

        if ($result === true) {
            return ['Đăng ký học phần thành công!', ''];
        } elseif ($result === 'duplicate') {
            return ['', 'Bạn đã đăng ký lớp học phần này rồi.'];
        } elseif ($result === 'full') {
            return ['', 'Lớp học phần đã đủ số lượng.'];
        }

        return ['', 'Đăng ký thất bại.'];
    }

    private function handleCancel(): array
    {
        // [LAYER 3] Action-level permission check
        if (!$this->auth->hasPermission('enrollment.cancel')) {
            return ['', 'Bạn không có quyền hủy đăng ký học phần.'];
        }

        $enrollmentId = (int)($_POST['enrollment_id'] ?? 0);

        if ($this->model->cancelEnrollment($enrollmentId, $this->studentId)) {
            return ['Hủy đăng ký thành công.', ''];
        }
        return ['', 'Không thể hủy. Học phần này không ở trạng thái "Đang học" hoặc không thuộc về bạn.'];
    }
}
// Registered
