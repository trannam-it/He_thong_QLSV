<?php
/**
 * ProfileController - Quản lý thông tin cá nhân và đổi mật khẩu
 *
 * Permission flow:
 *   [L1] Router: RBACMiddleware::check → 'profile.view'
 *   [L2] Controller: $this->requirePermission('profile.view')
 *   [L3] Update: 'profile.edit'
 */
class ProfileController extends BaseStudentController
{
    public function index(): void
    {
        // [LAYER 2] Controller permission check
        $this->requirePermission('profile.view');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'update_info';

            if ($action === 'update_info') {
                $this->handleUpdateInfo();
            } elseif ($action === 'change_password') {
                $this->handleChangePassword();
            }
        }

        $gpaData   = $this->model->calculateGPA($this->studentId);
        $activeTab = $_GET['tab'] ?? ($this->getFlash('active_tab') ?? 'info');
        unset($_SESSION['active_tab']);

        $this->render('profile/index.php', [
            'pageTitle' => 'Thông tin cá nhân',
            'student'   => $this->student,   // THÊM DÒNG NÀY
            'gpaData'   => $gpaData,
            'activeTab' => $activeTab,
            'success'   => $this->getFlash('success'),
            'error'     => $this->getFlash('error'),
            // Quyền cho view
            'canEdit'   => $this->auth->hasPermission('profile.edit'),
        ]);
    }

    private function handleUpdateInfo(): void
    {
        // [LAYER 3] Action-level check
        if (!$this->auth->hasPermission('profile.edit')) {
            $this->redirectWithMessage(BASE_URL . '/student/?page=profile', 'error', 'Bạn không có quyền cập nhật thông tin.');
            return;
        }

        $phone  = trim($_POST['phone'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors[] = 'Số điện thoại phải là 10 chữ số';
        }

        if (empty($errors)) {
            if ($this->model->updateContactInfo($this->studentId, $phone, $email)) {
                $this->student = $this->model->getOverviewByUserId($this->userId);
                $this->redirectWithMessage(BASE_URL . '/student/?page=profile', 'success', 'Cập nhật thông tin thành công!');
            } else {
                $errors[] = 'Có lỗi khi cập nhật';
            }
        }

        if (!empty($errors)) {
            $this->redirectWithMessage(BASE_URL . '/student/?page=profile', 'error', implode('. ', $errors));
        }
    }

    private function handleChangePassword(): void
    {
        $curPwd  = $_POST['current_password'] ?? '';
        $newPwd  = $_POST['new_password']     ?? '';
        $confPwd = $_POST['confirm_password'] ?? '';
        $errors  = [];

        $hash = $this->model->getPasswordHash($this->userId);
        if (!$hash || !password_verify($curPwd, $hash)) {
            $errors[] = 'Mật khẩu hiện tại không đúng';
        }
        if (strlen($newPwd) < 6) {
            $errors[] = 'Mật khẩu mới ít nhất 6 ký tự';
        }
        if ($newPwd !== $confPwd) {
            $errors[] = 'Xác nhận mật khẩu không khớp';
        }

        if (empty($errors)) {
            $newHash = password_hash($newPwd, PASSWORD_DEFAULT);
            if ($this->model->changePassword($this->userId, $newHash)) {
                $_SESSION['active_tab'] = 'password';
                $this->redirectWithMessage(BASE_URL . '/student/?page=profile&tab=password', 'success', 'Đổi mật khẩu thành công!');
            } else {
                $errors[] = 'Có lỗi khi đổi mật khẩu';
            }
        }

        if (!empty($errors)) {
            $_SESSION['active_tab'] = 'password';
            $this->redirectWithMessage(BASE_URL . '/student/?page=profile&tab=password', 'error', implode('. ', $errors));
        }
    }
}
