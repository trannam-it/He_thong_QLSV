<?php

class StudentRegisterController extends BaseStudentController
{
public function register(): void
{
    // 🔐 Kiểm tra đăng nhập
    if (!$this->auth->check()) {
        header("Location: " . BASE_URL . "/login");
        exit;
    }

    // ======================
    // XỬ LÝ POST
    // ======================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $studentId = $this->auth->getUserId();
        $classId   = (int)($_POST['class_id'] ?? 0);

        if ($classId <= 0) {
            $this->setFlash('error', 'Lớp không hợp lệ.');
            header("Location: " . StudentRouter::url('register'));
            exit;
        }

        $result = $this->model->registerClass($studentId, $classId);

        if ($result === true) {
            $this->setFlash('success', 'Đăng ký thành công!');
        } elseif ($result === 'duplicate') {
            $this->setFlash('error', 'Bạn đã đăng ký lớp này rồi.');
        } elseif ($result === 'full') {
            $this->setFlash('error', 'Lớp đã đủ số lượng.');
        } else {
            $this->setFlash('error', 'Đăng ký thất bại.');
        }

        header("Location: " . StudentRouter::url('register'));
        exit;
    }

    // ======================
    // LOAD DANH SÁCH LỚP
    // ======================
    $classes = $this->model->getAvailableClasses();

    $this->render('register/index.php', [
        'classes' => $classes,
        'success' => $this->getFlash('success'),
        'error'   => $this->getFlash('error'),
    ]);
}
}